<?php
class Encryption {
    public static function encrypt($message, $key) {
        if (empty($key)) {
            return $message;
        }
        
        $key = substr(hash('sha256', $key, true), 0, 32);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($message, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($encryptedMessage, $key) {
        if (empty($key)) {
            return $encryptedMessage;
        }
        
        $key = substr(hash('sha256', $key, true), 0, 32);
        $data = base64_decode($encryptedMessage);
        
        if (strlen($data) < 16) {
            throw new Exception("Invalid encrypted data");
        }
        
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        if ($decrypted === false) {
            throw new Exception("Decryption failed. Incorrect key or corrupted data.");
        }
        
        return $decrypted;
    }
}
?>
