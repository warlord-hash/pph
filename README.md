# pph
A simple transpiler that makes writing PHP easier.

## Reminder
PPH is currently in development, so there is a possibility of encountering bugs. I also want to add more features so people can use for example more helpers, or just make PPH a better experience, but I decided to release it like this for now.

## How to install
To install PPH (the CLI is built in), create a directory and then clone the repository by running ``git clone https://github.com/warlord-hash/pph.git``.

After that, go in the directory you cloned this repository into, and run ``composer install`` to fetch all dependencies.

## How to use
To use the CLI and compile a file, please use this command:

```
php C:/clonedRepoDirectoryHere/pph compile in=srcfolderhere out=compiledfilesdirectory --alt-explode
```

or, you can use it like this (if you opened powershell in the directory you cloned the repository to, or if you just used the cd command to get to it):
```
php pph compile in=srcfolderhere out=compiledfilesdirectory --alt-explode
```

Explanation:
* ``php`` - PHP.exe as provided in the PATH envieroment variable
* ``C:/clonedRepoDirectoryHere/pph`` - the directory where you cloned the repository into
* ``compile`` - the PPH CLI compile command
* ``in=srcfolderhere`` - parameter which takes in a directory where the pph files are stored (can be a full path, for example: ``D:\pph-tests``)
* ``out=compiledfilesdirectory`` - parameter which takes in a directory where the pph files will be saved to as php files (can be a full path, for example: ``D:\pph-tests``)
* ``alt-explode`` - an optional flag, if without the flag the files are missing semicolons or any other helpers please use this flag

## Syntax overview
A simple code sample:
```
echo "hello"
echo "test"

class Test {
    fun test() {
        echo "hi"
    }
}

$test = new Test()
$test->test()
```

From that example, we can say that:
* PHP tags (``<?php`` and ``?>``) are **no longer required to be written in the file**. PPH's transpiler will put them in for you.
* Semicolons are no longer required, but can be optionally put at the end of any line.
* Functions are now written as "fun" (although can still be written as "function")

A few  things are ommited from that sample, such as:
* The ``#helper-stop`` and ``#helper-start`` lines. They're the lines responsible for currently transform 'fun' into a function. By default, helpers are enabled but you can disable them by writing ``#helper-stop``. To allow helpers again, you can use ``#helper-start``.
* The ``#php-end`` line, this will instead at the end of the file put a PHP ending tag (``?>``) on the line you used this helper. (this is not affected by ``#helper-stop``)
