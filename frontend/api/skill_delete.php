<?php
header('Content-Type: application/json');
require_once '../lib/SkillRepository.php';
$r=new SkillRepository();
$db=$r->load();
$uuid=$_GET['uuid'] ?? '';
$db['skills']=array_values(array_filter($db['skills'],fn($s)=>$s['uuid']!=$uuid));
$db['links']=array_values(array_filter($db['links'],fn($l)=>$l['parent']!=$uuid && $l['child']!=$uuid));
$r->save($db);
echo json_encode(['status'=>'ok']);
