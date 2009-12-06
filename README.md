# Kohana v3 TwitterOAuth Module

### Installation:

1. go to [twitter's "Register an Application"](http://twitter.com/apps/new)
2. make sure your callback url looks like this: `http://your.server/twitter/registered`
3. once you are done, you will get a **Consumer key** and **Consumer secret**
5. enable the `twitteroauth` module in the `bootstrap.php`
6. go to the `twitteroauth/config/twitteroauth.php` and fill it out (use exact the same callback url)
7. go to your browser and call the `twitter/login` link
8. if everything worked fine, you should will be redirected to your `base-url`
9. now you can experiment with the **twitter api**, check out `usage-example.php` to get started