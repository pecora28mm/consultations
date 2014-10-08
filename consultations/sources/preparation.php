<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

class Preparation {
	public $consultation;
	public $consultations;
	public $member;

	function __construct() {
		$this->consultations = new Consultations();
	}
	
	function show_link_asking_for_email() {
		$html = "<div class=\"consultation-message-light\">";
		$html .= $this->link_asking_for_email();
		$html .= "</div>";
		return $html;
	}

	function link_asking_for_email() {
		return Html_Tag::a($this->url_to_asking_for_email(), __("Verify your current convocations"));
		
	}
	
	function url_to_asking_for_email() {
		return $GLOBALS['config']['url']."index.php?page=preparation.php";
	}
	
	function url_to_procedure() {
		return $GLOBALS['config']['url']."index.php?page=procedure.php";
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

	function send_convocation($member, $consultation) {
		require_once __DIR__."/../libraries/phpmailer/class.phpmailer.php";
		
		$mail = new PHPMailer();
		$mail->CharSet = "utf-8";
		$mail->AddAddress($member->email, $member->prenom." ".$member->nom);
		$mail->From = $GLOBALS['param']['preparation_email'];
		$mail->FromName = __("Consultations' tool");
		$mail->Subject = __("Consultation's convocation: %s", array($consultation->elements['question']));
		$mail->Body = __("Hello %s,", array($member->prenom))."\n\n";
		$mail->Body .= __("Please use the link below to vote about \"%s\":", array($consultation->elements['question']))."\n\n";
		$mail->Body .= $this->url_to_convocation($member, $consultation)."\n\n";
		$mail->Body .= __("The consultations team")."\n\n";

		if ($GLOBALS['config']['email_send']) {
			if (!empty($GLOBALS['config']['smtp_host'])) {
				$mail->IsSMTP();
				$mail->Host = $GLOBALS['config']['smtp_host'];
				$mail->Port = $GLOBALS['config']['smtp_port'];
				if (!empty($GLOBALS['config']['smtp_user'])) {
					$mail->SMTPAuth = true;
					$mail->Username = $GLOBALS['config']['smtp_user'];
					$mail->Password = $GLOBALS['config']['smtp_password'];
				} else {
					$mail->SMTPAuth = false;
				}
			}
			if ($mail->Send()) {
				return true;
			} else {
	 			return false;
			}
		} else {
			return false;
		}
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
		return md5($member->id."|".$consultation->id."|".$consultation->hash()."|".$GLOBALS['param']['preparation_secret']);
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
		
		$filters = array(
			'emails' => $this->member->email,
			'everyone' => 1,
		);
		
		$postcode = substr($this->member->codepostal, 0, 2);
		if (strlen($postcode) == 2) {
			$filters['postcode'] = $postcode;
		}
		
// 		$comity_ids = $this->member->comity_ids();
// 		if (count($comity_ids) > 0) {
// 			$filters['comity_id'] = $comity_ids;
// 		}
		
		$this->consultations = new Consultations();
		$this->consultations->day = time();
		$this->consultations->filters = $filters;
		$this->consultations->select();

		if (count($this->consultations) > 0) {
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
