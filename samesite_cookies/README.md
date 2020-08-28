SameSite Cookies for EE
========================

This addon allows you to add SameSite attribute to ExpressionEngine cookies.

For ExpressionEngine versions 3-5

All or individual cookies can be set as None, Lax or Strict. Simply enter which cookies you would like to apply SameSite cookie attribute to.

### Available Options: ###


#### Enforce site-wide SameSite cookie attribute ####
Here you may select which option to apply to the SameSite cookie attribute to all cookies.

- default
- None
- Lax
- Strict


#### Enter which cookies you would like to apply SameSite cookie paramter to ####
Here you would enter which cookies you would like to apply SameSite cookie attribute to. This would only apply when "Apply to entered cookies only" is selected.
Three text areas are available for these settings:

- None - Apply SameSite=None to entered cookies
- Lax - Apply SameSite=Lax to entered cookies
- Strict - Apply SameSite=Strict to entered cookies

Enter each cookie on a new line.


#### Make these cookies secure ####

Force the entered cookies to be secure. Browsers may block cookies set with 'SameSite=None' but without 'Secure'.
- Yes
- No


### About SameSite cookies and Chrome: ###

With recent changes to Google Chrome, cookies are now defaulted to SameSite=Lax. This may cause issues when attempting to send cookies to an external site.

This addon can resolve issues where offsite cookies are required, such as offsite payment gateways and tracking that requires cross-site cookies.

Note that not all browsers are compatible with SameSite=None. This addon will handle those that are not compatible and leave the parameter blank.
https://www.chromium.org/updates/same-site/incompatible-clients

