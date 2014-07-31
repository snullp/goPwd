#goPwd
####A password manager based on Google Authentication

I'm in early beta stage, do not use me.

##Basic idea

What if a password manager does not have a database behind? 

To prevent the danger from database leakage, one must encrypt everything in the database with a MasterKey or sth like that. Thus knowing the content of the database without the MasterKey equals nothing.

But, if we have a MasterKey, why would we need a database?

This tool tends to use your Google Account as a MasterKey. Once you've loggin in it will generate a MasterKey unique to your account (And also unique to YOUR OWN instance of this tool). And it will generate the password based on the name of the entity and the MasterKey per request, without storing anything permanently.

We want to keep every flow simpler here to reduct the security risk. We are heavily relying on Google's OAuth but we assume that is secure enough.
