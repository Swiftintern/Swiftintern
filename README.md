### Swiftintern
Internship platform made on SwiftMVC framework

### Database Schema
Old database schema
![Old Schema](https://97737482b27309a5b4daff7d6fc6d47f0100ec30.googledrive.com/secure/AAyT6L-A1dhWZgyYjXsS3_OFBSjoQpO2Y7yjvyqlnkf7FOZoP18BdGHFiWCDxHDqQZYDK4vLA4IwIQbI2u62DmOckwiUGTebznheTjEG624HSHQbG83OImXsn8CWGpsSA0IywzPKFya0h-bxqprXd7uYg3vOKpDAGhkNcgErKyvalt7ag-Y4egnrHEWGBAaxCUaIRwZ371ifXB0JFDc9-eOBPql2l3svLnMCLBpWdIHewXnWAslKA4LCapFTcSAQk_Y3C4TJKACTdqrMNBhzwrc4jybHs9gF3jKcsuRLtX3wmksL00hnv9_K_O7weaZ6TY7KNOMgeXB8FMu1POJzj9c4K_RBmcuKAmfHcSJUVqLxnB6RYc4qbqMeTEAGObH-M5c3XVly8IAnsO_ylukDfwGaMa6OMl_5K7xVjX8ZVvklcGollJl7eZEJaAaZF4Kv2j6fRFvf9cWVzEkhsVm-v3LDkf56cUS3wNr0Tdk6lM3Q85fJ_BAUQ-lWq4xTUbZLNIC6ITmijwkehyoE1O2e7i0SXNxYMccIJxq19hHFNKmsrW1t0H30ZqiyJYMP9pvYXhUkkv9in82LsuUc0FSgNfU2TLGoCS8zAA==/host/0BwUyS5h96GJLfnV6VWZqQlVmdzZPX2hpZFZmVkQwcEJFTUZvekZzemxSTy1VQUtQbG1ySzg/swiftintern.png)

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
