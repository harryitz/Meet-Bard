<h1>Meet-Bard<img src="asset/img/logo.png" height="64" width="64" align="left"></img></h1><br/> 

[![Lint](https://poggit.pmmp.io/ci.shield/TaylorR47/Meet-Bard/Meet-Bard)](https://poggit.pmmp.io/ci/TaylorR47/Meet-Bard/Meet-Bard) 
[![Discord](https://img.shields.io/discord/1100650029573738508.svg?label=&logo=discord&logoColor=ffffff&color=7389D8&labelColor=6A7EC2)](https://discord.gg/yAhsgskaGy) 

**NOTICE:** This plugin branch is for PocketMine-MP 4. <br/> ✨ **a library for PMMP connect to Google Bard AI.** </div>

`composer require taylor/meet-bard`

# API Docs
## Authentication
1. Visit https://bard.google.com/
2. F12 for console
3. Session: `Application → Cookies → Copy the value of __Secure-1PSID cookie.`

## Usage
Simple usage looks like:
```php
use TaylorR\MeetBard\Bard;
use TaylorR\MeetBard\security\User;

$token = '';
$user = new User($token);
$bardai = new Bard($user);
$ask = $this->client->ask("Hello, how are you?");
var_dump($ask);
```

# Include in your plugin
If you use Poggit to build your plugin, you can add it to your .poggit.yml like so:
```yaml
projects:
  MyPlugin:
    path: ""
    libs:
      - src: TaylorR47/Meet-Bard/Meet-Bard
        version: ^1.0.0
```

# License
GPL-3.0 License. Please see [LICENSE](LICENSE) for more information.
