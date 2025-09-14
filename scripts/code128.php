<?php

class Code128 {
    const START_A = 103;
    const START_B = 104;
    const START_C = 105;
    const STOP = 106;

    const CODE_A = 101;
    const CODE_B = 100;
    const CODE_C = 99;

    private $data;
    private $codeset;
    private $barcode;

    public function __construct($data) {
        $this->data = $data;
        $this->codeset = $this->get_codeset($data);
        $this->barcode = $this->generate_barcode();
    }

    public function get_barcode() {
        return $this->barcode;
    }

    private function get_codeset($data) {
        if (preg_match('/^[0-9]+$/', $data)) {
            return 'C';
        } else {
            return 'B';
        }
    }

    private function generate_barcode() {
        $barcode = [];
        $sum = 0;
        $pos = 1;

        if ($this->codeset == 'B') {
            $barcode[] = self::START_B;
            $sum = self::START_B;

            foreach (str_split($this->data) as $char) {
                $code = $this->get_code_b($char);
                $barcode[] = $code;
                $sum += $code * $pos;
                $pos++;
            }
        } else { // Code C
            $barcode[] = self::START_C;
            $sum = self::START_C;

            for ($i = 0; $i < strlen($this->data); $i += 2) {
                $code = (int)substr($this->data, $i, 2);
                $barcode[] = $code;
                $sum += $code * $pos;
                $pos++;
            }
        }

        $checksum = $sum % 103;
        $barcode[] = $checksum;
        $barcode[] = self::STOP;

        return $barcode;
    }

    private function get_code_b($char) {
        $code_map = [
            ' ' => 0, '!' => 1, '"' => 2, '#' => 3, '$' => 4, '%' => 5, '&' => 6, '\'' => 7, '(' => 8, ')' => 9,
            '*' => 10, '+' => 11, ',' => 12, '-' => 13, '.' => 14, '/' => 15, '0' => 16, '1' => 17, '2' => 18, '3' => 19,
            '4' => 20, '5' => 21, '6' => 22, '7' => 23, '8' => 24, '9' => 25, ':' => 26, ';' => 27, '<' => 28, '=' => 29,
            '>' => 30, '?' => 31, '@' => 32, 'A' => 33, 'B' => 34, 'C' => 35, 'D' => 36, 'E' => 37, 'F' => 38, 'G' => 39,
            'H' => 40, 'I' => 41, 'J' => 42, 'K' => 43, 'L' => 44, 'M' => 45, 'N' => 46, 'O' => 47, 'P' => 48, 'Q' => 49,
            'R' => 50, 'S' => 51, 'T' => 52, 'U' => 53, 'V' => 54, 'W' => 55, 'X' => 56, 'Y' => 57, 'Z' => 58, '[' => 59,
            '\\' => 60, ']' => 61, '^' => 62, '_' => 63, '`' => 64, 'a' => 65, 'b' => 66, 'c' => 67, 'd' => 68, 'e' => 69,
            'f' => 70, 'g' => 71, 'h' => 72, 'i' => 73, 'j' => 74, 'k' => 75, 'l' => 76, 'm' => 77, 'n' => 78, 'o' => 79,
            'p' => 80, 'q' => 81, 'r' => 82, 's' => 83, 't' => 84, 'u' => 85, 'v' => 86, 'w' => 87, 'x' => 88, 'y' => 89,
            'z' => 90, '{' => 91, '|' => 92, '}' => 93, '~' => 94, '\x7f' => 95
        ];

        return $code_map[$char] ?? 0;
    }

    public function get_image($height = 60, $width_multiplier = 2) {
        $patterns = [
            '212222', '222122', '222221', '121223', '121322', '131222', '122213', '122312', '132212', '221213',
            '221312', '231212', '112232', '122132', '122231', '113221', '123122', '123221', '223211', '221132',
            '221231', '213212', '223112', '312131', '311222', '321122', '321221', '312212', '322112', '322211',
            '212123', '212321', '232121', '111323', '131123', '131321', '112313', '132113', '132311', '211313',
            '231113', '231311', '112133', '112331', '132131', '113123', '113321', '133121', '313121', '211331',
            '231131', '213113', '213311', '213131', '311123', '311321', '331121', '312113', '312311', '332111',
            '314111', '221411', '413111', '111224', '111422', '121124', '121421', '141122', '141221', '112214',
            '112412', '122114', '122411', '142112', '142211', '241211', '221114', '412112', '411212', '212141',
            '214121', '412121', '111143', '111341', '131141', '114113', '114311', '411113', '411311', '113141',
            '114131', '311141', '411131', '211232', '211412', '211212', '2331112'
        ];

        $image_width = 0;
        foreach ($this->barcode as $code) {
            if (isset($patterns[$code])) {
                $pattern = $patterns[$code];
                for ($i = 0; $i < 6; $i++) {
                    $image_width += (int)$pattern[$i] * $width_multiplier;
                }
            }
        }

        $image = imagecreate($image_width, $height);
        $bg_color = imagecolorallocate($image, 255, 255, 255);
        $bar_color = imagecolorallocate($image, 0, 0, 0);

        
        $x = 0;
        foreach ($this->barcode as $code) {
            if (isset($patterns[$code])) {
                $pattern = $patterns[$code];
                for ($i = 0; $i < 6; $i++) {
                    $bar_width = (int)$pattern[$i] * $width_multiplier;
                    if ($i % 2 == 0) {
                        imagefilledrectangle($image, $x, 0, $x + $bar_width - 1, $height, $bar_color);
                    }
                    $x += $bar_width;
                }
            }
        }

        ob_start();
        imagepng($image);
        $image_data = ob_get_clean();
        imagedestroy($image);

        return $image_data;
    }
}
?>