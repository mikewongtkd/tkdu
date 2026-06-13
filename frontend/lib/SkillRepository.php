<?php
class SkillRepository {
    private string $file;
    public function __construct( $file = "/usr/local/tkdu/data/skills.json" ){
        $this->file=$file;
    }
    public function load(): array {
        return json_decode(file_get_contents($this->file),true);
    }
    public function save(array $data): void {
        $fp=fopen($this->file,'c+');
        flock($fp,LOCK_EX);
        ftruncate($fp,0);
        fwrite($fp,json_encode($data,JSON_PRETTY_PRINT));
        fflush($fp);
        flock($fp,LOCK_UN);
        fclose($fp);
    }
}
