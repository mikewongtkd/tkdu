<?php
header('Content-Type: application/json');
require_once '../lib/SkillRepository.php';
$r=new SkillRepository();
echo json_encode($r->load());
