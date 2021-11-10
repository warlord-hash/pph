<?php
namespace Core;

class Transpiler {
    public static $code;
    public static $semicolonAwareChars = [
        "}",
        "{",
        ",",
        ";",
        "//",
        "/*",
        "*",
        "#"
    ];

    public static $split;

    public static function openFile($name) {
        self::$code = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "$name.pph");
        self::$split = explode(PHP_EOL, self::$code);
    }

    public static function phpTags() {
        self::$code = "<?php" . PHP_EOL . self::$code . PHP_EOL . "?>";
    }

    public static function addSemicolons() {
        $in_array = false;

        $newCode = "";

        foreach(self::$split as $b) {
            if(strpos($b, "[") !== FALSE) $in_array = true;
            if(strpos($b, "]") !== FALSE) $in_array = false;

            if(strlen($b) == 0) {
                $b = $b . PHP_EOL;
            } else {
                $use = true;

                foreach(self::$semicolonAwareChars as $i) {
                    if(strpos($b, $i) !== FALSE) {
                        $use = false;
                        break;
                    }
                }

                if($in_array) $use = false;

                if($use) $b = $b . ";" . PHP_EOL;
                else $b = $b . PHP_EOL;

                $newCode = $newCode . $b;
            }
        }

        self::$code = $newCode;
    }

    public static function helpers() {
        $stopped = false;
        foreach(self::$split as $b) {
            if(strlen($b) > 0) {
                if(strpos("#helper-stop", $b) !== FALSE) $stopped = true;
                else if(strpos("#helper-start", $b) !== FALSE) $stopped = false;
            }
        }

        if($stopped === FALSE) {
            self::$code = str_replace("fun", "function", self::$code);
        }
     }

     public static function compile($printer, $inDir, $outDir) {
         // yes im aware document_root wont work lol
         $files = glob($_SERVER["DOCUMENT_ROOT"] . $inDir . "/*.{pph}", GLOB_BRACE);

         foreach($files as $file) {
            $no_dir = str_replace($_SERVER["DOCUMENT_ROOT"], "", $file);
            $filename = str_replace(".pph", "", $no_dir);

            $no_in = str_replace($inDir, "", $filename);

            $code = self::openFile($filename);
            self::addSemicolons();
            self::phpTags();
            self::helpers();

            if(file_put_contents($_SERVER["DOCUMENT_ROOT"] . $outDir . $no_in . ".php", self::$code)) {
                $printer->success("Compiled $no_dir");
            }
         }

         $subd = glob($_SERVER["DOCUMENT_ROOT"] . $inDir . "/*", GLOB_ONLYDIR);

         foreach($subd as $d) {
            $files = glob("$d/*.{pph}", GLOB_BRACE);

            if(count($files) > 0) {
                $clean_d = str_replace($_SERVER["DOCUMENT_ROOT"]. $inDir, "", $d);
                $new_dir = $_SERVER["DOCUMENT_ROOT"] . $outDir . "/$clean_d";

                if(!file_exists($new_dir)) mkdir($_SERVER["DOCUMENT_ROOT"] . $outDir . "/$clean_d", 0700);
            }

            foreach($files as $file) {
                $no_dir = str_replace($_SERVER["DOCUMENT_ROOT"], "", $file);
                $filename = str_replace(".pph", "", $no_dir);
                $no_in = str_replace($inDir, "", $filename);

                $code = self::openFile($filename);
                self::addSemicolons();
                self::phpTags();
                self::helpers();

                if(file_put_contents($outDir . $no_in . ".php", self::$code)) {
                    $printer->success("Compiled $no_dir");
                }
            }
        }
     }
}