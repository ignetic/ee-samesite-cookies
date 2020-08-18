SameSite Cookies for EE
========================

This addon allows you to add SameSite attribute to ExpressionEngine cookies.

For ExpressionEngine versions 3-5

All or individual cookies can be set as None, Lax or Strict. Simply enter which cookies you would like to apply SameSite cookie attribute to.

### Available Options: ###

#### Select SameSite cookie parameter ####
Here you may select which option to apply to the SameSite cookie attribute.

- default
- None
- Lax
- Strict

#### Make these cookies secure ####

Force the entered cookies to be secure. Browsers may block cookies set with 'SameSite=None' but without 'Secure'.
- Yes
- No

#### Apply this to all cookies or to just the entered ones below ####

- Apply to entered cookies only: 
- Apply to ALL site-wide cookies (this applies SameSite attribute to all cookies on the site)

#### Enter which cookies you would like to apply SameSite cookie paramter to ####

Here you would enter into a textarea which cookies you would like to apply SameSite cookie attribute to. This would only apply when "Apply to entered cookies only" is selected.
- Enter each cookie on a new line


### About SameSite cookies and Chrome: ###

With recent changes to Google Chrome, cookies are now defaulted to SameSite=Lax. This may cause issues when attempting to send cookies to an external site.

This addon can resolve issues where offsite cookies are required, such as offsite payment gateways and with 3D Secure.

Note that not all browsers are compatible with SameSite=None. This addon will handle those that are not compatible and leave the parameter blank.
https://www.chromium.org/updates/same-site/incompatible-clients

