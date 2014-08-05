#goPwd
####A password manager framework based on Google Authentication
Note: this framework intents to be used in a self-deployed environment. Do not use it if you don't trust its provider.

##Basic idea

We don't really need a database for a password manager since we can always have the password by putting a MasterKey and the entity's name to an algorithm with no randomness.

This framework tends to use your Google Account as a MasterKey. Once you've loggin in it will generate a MasterKey unique to your account (And also unique to YOUR OWN instance of this tool). And it will generate the password based on the name of the entity and the MasterKey per request, without storing anything permanently (Or it may store some configurations but all encrypted in DES with the MasterKey).

We want to keep every flow simpler here to reduce the security risk. We are heavily relying on Google's OAuth but we assume that is secure enough.

##Customizable password generator
The problem with other password managers is about their password generation procdure. Usually the generated password is almost impossible to be manually keyed in on another device (sometimes due to no native app available), e.g. a BlackBerry device.

So by making the password generator customizable and selectable, one can create a generator by his or her preferences. For example, the dict.pwdGen.php that provided in this repo will generate human-friendly password, which is much easier to read and type.

##Development guide
The framework itself (include/goPwd.php) provides interfaces that can invoke Google's OAuth 2.0 authentication as well as call a password generator. Also it can save and restore website configurations per user in include/Configs.

The OAuth API file (api/auth.php) receives what Google returned to us and do necessary processing.

The passwords generators (include/Generators) are the actual algorithms that turn the name of the entity and the master key to the password. All current generators are only examples and not considered secure.

A generator takes 3 parameters: the name of the entity, the masterkey and an options array. The generator returns a string as the generated password.

