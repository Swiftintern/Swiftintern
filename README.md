## Swiftintern ##
Internship platform made on SwiftMVC framework with features of students, employer, organization profiling and placement papers, online test, resume creator

### Libraries Used: ###
- [Imagine] (https://github.com/avalanche123/Imagine)
- [Guzzle] (https://github.com/guzzle/guzzle)
- [MailGun] (https://github.com/mailgun/mailgun-php)
- [Sendgrid] (https://github.com/sendgrid/sendgrid-php)
- [Smtpapi] (https://github.com/sendgrid/smtpapi-php)
- Fonts

### Plugins Used: ###
- Logger(Logs events in application)
- SEO (setting up meta tags for website)
 
### Under Development ###
- JavaScript MVC
- API

### Database Schema ###
Old database schema
![Old Schema](https://github.com/faizanayubi/swiftintern/blob/master/application/db/swiftintern.png?raw=true)

New Schema Changes
- messages table (Split it in two because of relevation in mails sent and newsletter etc)
    - conversation
        - user_id
        - property
        - property_id
        - message_id
        - validity
        - created
    - messages
        - subject
        - body
        - created
- metas table (to store meta data)
    - name
    - value
    - property
    - property_id
    - created
