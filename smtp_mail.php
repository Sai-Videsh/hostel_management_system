<?php
/**
 * Simple SMTP mailer class
 */
class SMTPMailer {
    private $smtp_host;
    private $smtp_port;
    private $smtp_user;
    private $smtp_pass;
    private $from_email;
    private $from_name;
    private $debug = false;

    /**
     * Constructor
     */
    public function __construct($smtp_host, $smtp_port, $smtp_user, $smtp_pass) {
        $this->smtp_host = $smtp_host;
        $this->smtp_port = $smtp_port;
        $this->smtp_user = $smtp_user;
        $this->smtp_pass = $smtp_pass;
        $this->from_email = $smtp_user;
        $this->from_name = "Hostel Management System";
    }

    /**
     * Enable debug mode
     */
    public function setDebug($debug) {
        $this->debug = $debug;
    }

    /**
     * Set sender information
     */
    public function setFrom($email, $name = "") {
        $this->from_email = $email;
        if (!empty($name)) {
            $this->from_name = $name;
        }
    }

    /**
     * Send email
     */
    public function send($to, $subject, $body, $is_html = true) {
        // Generate a unique boundary
        $boundary = md5(time());
        
        // Connect to SMTP server
        $smtp = fsockopen($this->smtp_host, $this->smtp_port, $errno, $errstr, 30);
        if (!$smtp) {
            $this->debugLog("Connection failed: $errstr ($errno)");
            return false;
        }

        // Check initial response
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '220') {
            $this->debugLog("Server not ready: $response");
            return false;
        }

        // Say HELO
        fputs($smtp, "EHLO ".$_SERVER['SERVER_NAME']."\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '250') {
            $this->debugLog("EHLO failed: $response");
            return false;
        }
        
        // Clear buffer
        while(substr($response, 3, 1) != ' ') {
            $response = fgets($smtp, 515);
        }

        // Request STARTTLS
        fputs($smtp, "STARTTLS\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '220') {
            $this->debugLog("STARTTLS failed: $response");
            return false;
        }

        // Enable TLS encryption
        stream_socket_enable_crypto($smtp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);

        // Say HELO again after TLS
        fputs($smtp, "EHLO ".$_SERVER['SERVER_NAME']."\r\n");
        $response = fgets($smtp, 515);
        
        // Clear buffer
        while(substr($response, 3, 1) != ' ') {
            $response = fgets($smtp, 515);
        }

        // Auth login
        fputs($smtp, "AUTH LOGIN\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '334') {
            $this->debugLog("AUTH failed: $response");
            return false;
        }

        // Send username
        fputs($smtp, base64_encode($this->smtp_user)."\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '334') {
            $this->debugLog("Username rejected: $response");
            return false;
        }

        // Send password
        fputs($smtp, base64_encode($this->smtp_pass)."\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '235') {
            $this->debugLog("Authentication failed: $response");
            return false;
        }

        // Set mail from
        fputs($smtp, "MAIL FROM: <".$this->from_email.">\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '250') {
            $this->debugLog("MAIL FROM failed: $response");
            return false;
        }

        // Set recipient
        fputs($smtp, "RCPT TO: <".$to.">\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '250') {
            $this->debugLog("RCPT TO failed: $response");
            return false;
        }

        // Send DATA command
        fputs($smtp, "DATA\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '354') {
            $this->debugLog("DATA failed: $response");
            return false;
        }

        // Prepare headers
        $headers = "From: ".$this->from_name." <".$this->from_email.">\r\n";
        $headers .= "Reply-To: ".$this->from_email."\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        
        if ($is_html) {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        // Send email content
        fputs($smtp, "Subject: $subject\r\n");
        fputs($smtp, $headers."\r\n");
        fputs($smtp, $body."\r\n");

        // End data
        fputs($smtp, ".\r\n");
        $response = fgets($smtp, 515);
        if (substr($response, 0, 3) != '250') {
            $this->debugLog("Message sending failed: $response");
            return false;
        }

        // Quit
        fputs($smtp, "QUIT\r\n");
        fclose($smtp);
        
        return true;
    }
    
    /**
     * Log debug messages
     */
    private function debugLog($message) {
        if ($this->debug) {
            error_log("[SMTP] " . $message);
        }
    }
}
?>