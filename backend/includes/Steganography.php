<?php
class Steganography {
    private $image;
    private $width;
    private $height;

    public function __construct($imagePath) {
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            throw new Exception("Invalid image file");
        }

        $this->width = $imageInfo[0];
        $this->height = $imageInfo[1];

        switch ($imageInfo[2]) {
            case IMAGETYPE_PNG:
                $this->image = imagecreatefrompng($imagePath);
                break;
            case IMAGETYPE_JPEG:
                $this->image = imagecreatefromjpeg($imagePath);
                break;
            case IMAGETYPE_BMP:
                $this->image = imagecreatefrombmp($imagePath);
                break;
            default:
                throw new Exception("Unsupported image format");
        }
    }

    public function embedMessage($message) {
        $binaryMessage = $this->stringToBinary($message);
        $messageLength = strlen($binaryMessage);
        $lengthBinary = str_pad(decbin($messageLength), 32, '0', STR_PAD_LEFT);
        
        $totalBits = $messageLength + 32;
        $maxBits = $this->width * $this->height * 3;
        
        if ($totalBits > $maxBits) {
            throw new Exception("Message too large for this image");
        }

        $bitIndex = 0;
        
        // Embed message length first
        for ($i = 0; $i < 32; $i++) {
            $pixel = $this->getPixelCoordinates($bitIndex);
            $rgb = $this->getPixelRGB($pixel['x'], $pixel['y']);
            $channel = $bitIndex % 3;
            $rgb[$channel] = ($rgb[$channel] & 0xFE) | intval($lengthBinary[$i]);
            $this->setPixelRGB($pixel['x'], $pixel['y'], $rgb);
            $bitIndex++;
        }
        
        // Embed actual message
        for ($i = 0; $i < $messageLength; $i++) {
            $pixel = $this->getPixelCoordinates($bitIndex);
            $rgb = $this->getPixelRGB($pixel['x'], $pixel['y']);
            $channel = $bitIndex % 3;
            $rgb[$channel] = ($rgb[$channel] & 0xFE) | intval($binaryMessage[$i]);
            $this->setPixelRGB($pixel['x'], $pixel['y'], $rgb);
            $bitIndex++;
        }
    }

    public function extractMessage() {
        $binaryLength = '';
        
        // Extract message length (first 32 bits)
        for ($i = 0; $i < 32; $i++) {
            $pixel = $this->getPixelCoordinates($i);
            $rgb = $this->getPixelRGB($pixel['x'], $pixel['y']);
            $channel = $i % 3;
            $binaryLength .= ($rgb[$channel] & 1);
        }
        
        $messageLength = bindec($binaryLength);
        
        // Extract actual message
        $binaryMessage = '';
        for ($i = 32; $i < 32 + $messageLength; $i++) {
            $pixel = $this->getPixelCoordinates($i);
            $rgb = $this->getPixelRGB($pixel['x'], $pixel['y']);
            $channel = $i % 3;
            $binaryMessage .= ($rgb[$channel] & 1);
        }
        
        return $this->binaryToString($binaryMessage);
    }

    public function saveImage($outputPath, $imageType = IMAGETYPE_PNG) {
        switch ($imageType) {
            case IMAGETYPE_PNG:
                imagepng($this->image, $outputPath);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($this->image, $outputPath, 90);
                break;
            case IMAGETYPE_BMP:
                imagebmp($this->image, $outputPath);
                break;
        }
    }

    private function stringToBinary($string) {
        $binary = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = ord($string[$i]);
            $binary .= str_pad(decbin($char), 8, '0', STR_PAD_LEFT);
        }
        return $binary;
    }

    private function binaryToString($binary) {
        $string = '';
        for ($i = 0; $i < strlen($binary); $i += 8) {
            $byte = substr($binary, $i, 8);
            if (strlen($byte) == 8) {
                $string .= chr(bindec($byte));
            }
        }
        return $string;
    }

    private function getPixelCoordinates($bitIndex) {
        $pixelIndex = floor($bitIndex / 3);
        $x = $pixelIndex % $this->width;
        $y = floor($pixelIndex / $this->width);
        return ['x' => $x, 'y' => $y];
    }

    private function getPixelRGB($x, $y) {
        $color = imagecolorat($this->image, $x, $y);
        return [
            ($color >> 16) & 0xFF, // Red
            ($color >> 8) & 0xFF,  // Green
            $color & 0xFF          // Blue
        ];
    }

    private function setPixelRGB($x, $y, $rgb) {
        $color = imagecolorallocate($this->image, $rgb[0], $rgb[1], $rgb[2]);
        imagesetpixel($this->image, $x, $y, $color);
    }

    public function __destruct() {
        if ($this->image) {
            imagedestroy($this->image);
        }
    }
}
?>
