# loop-read-clipboard

## What is it?

- tool to loop and read content of clipboard, (then optionally write them to a file)
- tested on windows, not tested on linux

## Usage
```
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
```
