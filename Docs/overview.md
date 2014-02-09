# Using the Authentication Package

The authentication package provides a decoupled authentication system for providing authentication in your 
application.  Authentication strategies are swappable.  Currently only a simple local strategy is supported.

Authentication would generally be performed in your application by doing the following:

```
$credentialStore = array(
	'user1' => '$2y$12$QjSH496pcT5CEbzjD/vtVeH03tfHKFy36d4J0Ltp3lRtee9HDxY3K',
	'user2' => '$2y$12$QjSH496pcT5CEbzjD/vtVeH03tfHKFy36d4J0Ltp3lsdgnasdfasd'
)

$authentication = new Authentication;
$authentication->addStrategy('local', new LocalStrategy($input, $credentialStore));

// Username and password are retrieved using $input->get('username') and $input->get('password') respectively.
$username = $authentication->authenticate();                     // Try all strategies

$username = $authentication->authenticate(array('local'));       // Just use the 'local' strategy
```



## class `Authentication\Authentication`

### <> public addStrategy(String $strategyName, AuthenticationStrategyInterface $strategy)

Adds a strategy object to the list of available strategies to use for authentication.


### <> public authenticate($strategies = array())

Attempts authentication.  Uses all strategies if the array is empty, or a subset of one or more if provided.


### <> public getResults()

Gets a hashed array of strategies and authentication results.

```
$authentication->getResults();

/** Might return:
 array(
 	'local' => Authentication::SUCCESS,
 	'strategy2' => Authentication::MISSING_CREDENTIALS
 )
**/
```


## class `Authentication\Stragies\LocalStrategy`

Provides authentication support for local credentials obtained from a ```Joomla\Input\Input``` object.

```
$credentialStore = array(
	'jimbob' => 'agdj4345235',		// username and password hash
	'joe' => 'sdgjrly435235'		// username and password hash
)

$strategy = new Authentication\Strategies\LocalStrategy($input, $credentialStore);
```


## interface `Authentication\AuthenticationStrategyInterface`

### <> public authenticate()

This function must perform whatever actions are necessary to verify whether there are valid credentials.  The
credential source is generally determined by the object constructor where they get passed in as dependencies.
As an example, LocalStrategy takes an Input object and a hash of credential pairs.  The method should set the
set the status of the authentication attempt for retrieval from the getStatus() method.


### <> public getStatus()

This function should return the status of the last authentication attempt (specified using Authentication class
constants).
