<?php

$options = getopt("f::s::c::l::r::d::h::"); //var_dump($options);
if(!empty($options["f"])) $opt["file"] = $options["f"]; else $opt["file"] = "extracted-text.txt";
if(!empty($options["s"])) $opt["sleep"] = $options["s"]; else $opt["sleep"] = 1;
if(isset($options["c"])) $opt["concat"] = true; else $opt["concat"] = false; // on/off flag
if(isset($options["l"])) $opt["line"] = true; else $opt["line"] = false; // on/off flag
if(isset($options["h"])) $opt["help"] = true; else $opt["help"] = false; // on/off flag
if(!empty($options["r"])) $opt["regex"] = $options["r"]; else $opt["regex"] = false;
if(!empty($options["d"])) $opt["delete"] = $options["d"]; else $opt["delete"] = false;

//var_dump($opt);

if(!is_int($opt["sleep"])) {
    echo "sleep must be integer";
    exit;
}

if($opt["concat"] == false) {
    $fp = fopen($opt["file"], "a+"); // open file in append mode, create if not exist
}

if($opt["help"]) {
    echo "
        Usage: [-f FILEOUTPUT] [-s SLEEP] [-c] [-l]
            -f FILEOUTPUT = define file name to write the output to 
                (this is important since windows does not have command line output piping like bash)
                default to 'extracted-text.txt'
            -s SLEEP = define how often (in seconds) the script should read the clipboard
                default to 1 second
            -c = concat: when set, will not write output to file (just concat to terminal)
            -l = line: force captured text to be converted into 1 line (replace newline character with \\r and \\n)
            -r REGEX = regex string to be captured, if specified, must contain ONE capture group
            -d DELETE = regex string to be deleted from string
            -h = help: display this help text
        Example:
            1. capture first numeric occurence from clipboard, but your command line always outputs 'Active code page: 65001'
                php loop-read-clipboard.php -d'%Active code page: 65001\\\\n%' -l -c -r'%([0-9]+)%'
    "; exit;
}

//var_dump($opt); exit;

function getClipboard():string{
    if(PHP_OS_FAMILY==="Windows") {
    // works on windows 7 + (PowerShell v2 + )
    // TODO: is it -1 or -2 bytes? i think it was -2 on win7 and -1 on win10?
        //return substr(shell_exec('powershell -sta "add-type -as System.Windows.Forms; [windows.forms.clipboard]::GetText()"'),0,-2);
        return shell_exec('powershell -sta "Get-Clipboard"');
    } else if(PHP_OS_FAMILY==="Linux") {
        // untested! but should work on X.org-based linux GUI's
        return substr(shell_exec('xclip -out -selection primary'),0,-1);
    } else if(PHP_OS_FAMILY==="Darwin") {
        // untested! 
        return substr(shell_exec('pbpaste'),0,-1);
    } else {
        throw new \Exception("running on unsupported OS: ".PHP_OS_FAMILY." - only Windows, Linux, and MacOS supported.");
    }
}


$txt_last = "";
while (true) {
    sleep($opt["sleep"]);
    $txt_current = getClipboard();
    if($opt["line"] == true) {
        $txt_current = str_replace("\r", "\\r", $txt_current);
        $txt_current = str_replace("\n", "\\n", $txt_current);
    }
    if(!empty($opt["delete"])) {
        $txt_current = preg_replace($opt["delete"], "", $txt_current);
    }
    if(!empty($opt["regex"])) {
        preg_match($opt["regex"], $txt_current, $matches);
        if(empty($matches)) continue;
        $txt_current = $matches[0];
    }
    if($txt_last == $txt_current) {
        continue;
    } else {
        $txt_last = $txt_current;
        echo $txt_current."\n";
        if($opt["concat"] == false) {
            fwrite($fp, $txt_current."\n");
        }
    }
}

