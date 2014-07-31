<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Preparation {
	public $consultation;
	public $consultations;
	public $member;

	function __construct() {
		$this->consultations = new Consultations();
	}
	
	function link_asking_for_email() {
		return Html_Tag::a($this->url_to_asking_for_email(), __("Verify your current convocations"));
		
	}
	
	function url_to_asking_for_email() {
		return $GLOBALS['config']['url']."index.php?page=preparation.php";
	}
	
	function url_to_consultation() {
		return $GLOBALS['config']['url']."index.php?page=consultation.php";
	}
	
	function consultation_id() {
		if ($this->consultation instanceof Consultation) {
			return $this->consultation->id;
		} else {
			return false;
		}
	}

	function member_id() {
		if ($this->member instanceof Member) {
			return $this->member->id;
		} else {
			return false;
		}
	}
	
	function url_to_convocation($member, $consultation) {
		return $GLOBALS['config']['url']."?key=".$this->encode_member_consultation($member, $consultation);
	}

	function send_convocations_for_member($member = null) {
		if ($member !== null and $member instanceof Member) {
			$this->member = $member;
		}
		if (!($this->member instanceof Member)) {
			return false;
		}
		
		$result = true;
		foreach ($this->consultations as $consultation) {
			$result = (bool)($result and $this->send_convocation($this->member, $consultation));
		}
		return $result;
	}
	
	function send_convocations_for_consultation($consultation = null) {
		if ($consultation !== null and $consultation instanceof Consultation) {
			$this->consultation = $consultation;
		}
		if (!($this->consultation instanceof Consultation)) {
			return false;
		}
		
		$bigfichedb = new Db($GLOBALS['bigficheconfig']);
		
		$members = new Members($bigfichedb);
		$members->comity_id = (int)$this->consultation->comity_id;
		$members->select();
		
		$result = true;
		foreach ($members as $member) {
			$result = (bool)($result and $this->send_convocation($member, $this->consultation));
		}
		return $result;
	}
	
	function send_convocation($member, $consultation) {
		require_once __DIR__."/../libraries/phpmailer/class.phpmailer.php";
		
		$mail = new PHPMailer();
		$mail->AddAddress($member->email, $member->prenom." ".$member->nom);
		$mail->From = $GLOBALS['param']['preparation_email'];
		$mail->FromName = __("Consultations' tool");
		$mail->Subject = __("Consultation's convocation: %s", array($consultation->elements['question']));
		$mail->Body = __("Hello %s,", array($member->prenom))."\n\n";
		$mail->Body .= __("Please use the link below to vote about \"%s\"?", array($consultation->elements['question']))."\n\n";
		$mail->Body .= $this->url_to_convocation($member, $consultation)."\n\n";
		$mail->Body .= __("The consultations team")."\n\n";
debug::dump($member->email, $mail->Subject, $mail->Body);

		return $mail->Send();
	}
	
	function is_key_coherent($key) {
		$string = base64_decode($key);
		list($member_id, $consultation_id, $hash) = explode("|", $string);
		
		$consultation = new Consultation();
		$consultation->load($consultation_id);

		if ($this->hash_member_consultation(new Member($member_id), $consultation) == $hash) {
			return true;
		} else {
			return false;
		}
	}
	
	function hash_member_consultation(Member $member, Consultation $consultation) {
		return md5($member->id."|".$consultation->id."|".$consultation->hash()."|".$GLOBALS['config']['url']);
	}

	function encode_member_consultation(Member $member, Consultation $consultation) {
		$string = $member->id."|".$consultation->id."|".$this->hash_member_consultation($member, $consultation);
		$string = base64_encode($string);
		return $string;
	}
	
	function show_convocations_sent() {
		$html = "<div class=\"preparation-message\">";
		$html .= __("Your convocation(s) have been sent : please check your emails in order to proceed to the vote.");
		$html .= "</div>";
		
		return $html;
	}
	
	function show_missing_convocation() {
		$html = "<div class=\"preparation-error\">";
		$html .= __("Sorry, your convocation is missing.");
		$html .= "</div>";
		
		return $html;
	}
	
	function show_wrong_convocation() {
		$html = "<div class=\"preparation-error\">";
		$html .= __("Sorry, your convocation is not valid.");
		$html .= "</div>";
		
		return $html;
	}
	
	function show_error_while_sending_convocations() {
		$html = "<div class=\"preparation-error\">";
		$html .= __("Sorry, there was an error while sending your convocation by email.");
		$html .= "</div>";
		
		return $html;
	}

	function show_email_not_valid() {
		$html = "<div class=\"preparation-error\">";
		$html .= __("Sorry, your email is not valid.");
		$html .= "</div>";
		
		return $html;
	}

	function show_no_open_consultation() {
		$html = "<div class=\"preparation-error\">";
		$html .= __("Sorry, no consultation is currently accessible to you.");
		$html .= "</div>";
		
		return $html;
	}
	
	function is_ready() {
		if ($this->member->id > 0 and count($this->consultations) > 0) {
			return true;
		} else {
			return false;
		}
	}
	
	function is_email_valid($email) {
		return (preg_match("/[_a-z0-9-]+([\._a-z0-9-]+)*@[\._a-z0-9-]+(\.[a-z0-9-]{2,5})+/", $email));		
	}
	
	function charge_member_and_consultation_with_key($key, Db $db = null) {
		if ($this->is_key_coherent($key)) {
			$string = base64_decode($key);
			list($member_id, $consultation_id, $hash) = explode("|", $string);
			$this->member = new Member(0, $db);
 			$this->member->load($member_id);
			
			$this->consultation = new Consultation();
			$this->consultation->load($consultation_id);
			return true;
		} else {
			return false;
		}
	}

	function charge_member_with_email($email, Db $db = null) {
		if ($this->is_email_valid($email)) {
			$this->member = new Member(0, $db);
			$this->member->email = $email;
			if ($this->member->match_existing(array("email"))) {
				$this->member->load();
			}
			return true;
		} else {
			return false;
		}
	}
	
	function charge_open_consultations_for_member($member = null, Db $db = null) {
		if ($member !== null and $member instanceof Member) {
			$this->member = $member;
		}
		if (!($this->member instanceof Member)) {
			return false;
		}
		
		$comity_ids = $this->member->comity_ids();

		$this->consultations = new Consultations();
		if (count($comity_ids) > 0) {
			$this->consultations->comity_id = $comity_ids;
			$this->consultations->day = time();
			$this->consultations->select();
			return true;
		} else {
			return false;
		}
	}

	function ask_for_email() {
		$html = "<h1>".__("Let's see if you can vote")."</h1>";
		
		$html .= "<form method=\"post\" action=\"\">";
		
		$email = new Html_Input("preparation[email]", "", "email");
		$email->properties['placeholder'] = __("Email");
		$html .= $email->input();
		
		$verify = new Html_Input("preapration[verify]", __("Verify"), "submit");
		$verify->properties['class'] = "btn btn-success consultation-submit";
		$html .= $verify->input();
		
		$html .= "</form>";
		
		return $html;
	}
}
