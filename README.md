## HiSoRange Browser Detect package WITHOUT Laravel.
***

This is the same package as the hisorange/browser-detect package, but stripped from the Laravel requirement. Only the runtime cache is used. All credit goes to hisorange for this package. More information here: https://github.com/hisorange/browser-detect

Basic usage:

```php
use hisorange\BrowserDetect\Parser;

$parser = new Parser;
$info = $parser->detect();
```

If you want to change the default configs, pass the config array as the first parameter to the Parser class, and the plugin array as the second parameter:

```
$parser = new Parser($config, $plugins);
```