### Swiftintern
Internship platform made on SwiftMVC framework

### Database Schema
Old database schema
![Old Schema](http://googledrive.com/host/0BwUyS5h96GJLfnV6VWZqQlVmdzZPX2hpZFZmVkQwcEJFTUZvekZzemxSTy1VQUtQbG1ySzg/swiftintern.png)

New Schema Changes
- Split message table in two(because of relevation in mails sent and newsletter etc)
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
