<?php
/* Nouvelle Donne -- Copyright (C) Perrick Penet-Avez 2014 - 2014 */

$consultation = new Consultation();
echo $consultation->link_to_new();

$consultations = new Consultations();
$consultations->select();
echo $consultations->manage();