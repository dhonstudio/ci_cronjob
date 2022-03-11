<?php
defined('BASEPATH') OR exit('No direct script access allowed');

date_default_timezone_set('Asia/Jakarta');

class Email extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

        require_once __DIR__ . '/../../assets/ci_libraries/DhonAPI.php';
        $this->dhonapi = new DhonAPI;

        $this->load->helper('email_config');

        /*
        | -------------------------------------------------------------------
        |  Set up this API connection section
        | -------------------------------------------------------------------
        */
        $this->api_url = ENVIRONMENT == 'development' ? 'https://domain.com/ci/api/' : 'https://domain.com/ci/api/';
	
        /*
        | -------------------------------------------------------------------
        |  Set up this email and backup
        | -------------------------------------------------------------------
        */
        $this->email_address    = 'no-reply@domain.com';
        $this->email_sender     = 'Dhon Studio';
        $this->email_backup     = 'domain@domain.com';
        $this->backup_db_folder = './ci/api/backup/';
    }

	public function send_backup()
	{
        $dbs = explode(' ', $_GET['db']);

        foreach ($dbs as $db) {
            $this->dhonapi->curl($this->api_url."backupDB?db={$db}");
        }

        /*
        | -------------------------------------------------------------------
        | Don't forget to create email_config_helper.php on folder helpers
        | -------------------------------------------------------------------
        | Prototype:
        |
        | <?php
        | 
        | $ci = get_instance();
        | 
        | $ci->email_config = [
        |     'protocol'	=> 'smtp',
        |     'smtp_host'	=> 'ssl://srv.hosting.com',
        |     'smtp_user'	=> 'user@domain.com',
        |     'smtp_pass'	=> 'password',
        |     'smtp_port'	=> 465,
        |     'mailtype'	=> 'html',
        |     'charset'	    => 'utf-8',
        |     'newline'	    => "\r\n",
        |     'wordwrap'	=> TRUE,
        | ];
        */

        $this->email->initialize($this->email_config);
        $this->email->from($this->email_address, $this->email_sender);
        $this->email->to($this->email_backup);
        foreach ($dbs as $db) {
            $this->email->attach($this->backup_db_folder.'db_'.$db.'.sql');
        }
        
        $this->email->subject('Backup DB '.date('YmdHis', time()));
        $this->email->message('Backup DB '.date('YmdHis', time()));

        foreach ($dbs as $db) {
            unlink($this->backup_db_folder.'db_'.$db.'.sql');
        }

        if($this->email->send()) {
            return true;
        } else {
            echo $this->email->print_debugger();
            die;
        }
    }
}