# The Authentication Package

## Interfaces

### `Authentication\AuthenticationStrategyInterface`

Basic usage:

```
use Joomla\Authentication;

$authentication = new Authentication\Authentication;

$credentialStore = array(
	'jimbob' => 'agdj4345235',		// username and password hash
	'joe' => 'sdgjrly435235'		// username and password hash
)

$authentication->addStrategy(new Authentication\Strategies\LocalStrategy($input, ))

$username = $authentication->authenticate();   //  Try all authentication strategies
$username = $authentication->authenticate('local');

if ($username)
{
	// we are authenticated
	// Maybe we put the username in the session
}
```