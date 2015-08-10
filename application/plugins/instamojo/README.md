# Instamojo PHP API

Assists you to programmatically create, edit and delete Links on Instamojo in PHP.


## Usage

### Create a Link

    <?php
    require "instamojo.php";

    $api = new Instamojo('[API_KEY]', '[AUTH_TOKEN]');

    try {
        $response = $api->linkCreate(array(
            'title'=>'Hello API',
            'description'=>'Create a new Link easily',
            'base_price'=>100,
            'currency'=>'INR',
            'cover_image'=>'/path/to/photo.jpg'
            ));
        print_r($response);
    }
    catch (Exception $e) {
        print('Error: ' . $e->getMessage());
    }
    ?>

This will give you JSON object containing details of the Link that was just created.

### Edit a Link

    <?php
    require "instamojo.php";

    $api = new Instamojo('[API_KEY]', '[AUTH_TOKEN]');

    try {
        $response = $api->linkEdit(
            'hello-api', // You must specify the slug of the Link
            array(
            'title'=>'A New Title',
            ));
        print_r($response);
    }
    catch (Exception $e) {
        print('Error: ' . $e->getMessage());
    }
    ?>

### List all Links

    <?php
    require "instamojo.php";

    $api = new Instamojo('[API_KEY]', '[AUTH_TOKEN]');

    try {
        $response = $api->linksList();
        print_r($response);
    }
    catch (Exception $e) {
        print('Error: ' . $e->getMessage());
    }
    ?>

### List all Payments

    <?php
    require "instamojo.php";

    $api = new Instamojo('[API_KEY]', '[AUTH_TOKEN]');

    try {
        $response = $api->paymentsList();
        print_r($response);
    }
    catch (Exception $e) {
        print('Error: ' . $e->getMessage());
    }
    ?>

### Get Details of a Payment using Payment ID

    <?php
    require "instamojo.php";

    $api = new Instamojo('[API_KEY]', '[AUTH_TOKEN]');

    try {
        $response = $api->paymentDetail('[PAYMENT ID]');
        print_r($response);
    }
    catch (Exception $e) {
        print('Error: ' . $e->getMessage());
    }
    ?>


## Available Functions

You have these functions to interact with the API:

  * `linksList()` List all Links created by authenticated User.
  * `linkDetail($slug)` Get details of Link specified by its unique slug.
  * `linkCreate(array $link)` Create a new Link.
  * `linkEdit($slug, array $link)` Edit an existing Link.
  * `linkDelete($slug)` Archvive a Link - Archived Links cannot be generally accessed by the API. User can still view them on the Dashboard at instamojo.com.
  *  `paymentsList()` List all Payments linked to User's account.
  * `paymentDetail($payment_id)` Get details of a Payment specified by its unique Payment ID. You may receive the Payment ID via `paymentsList()` or via URL Redirect function or as a part of Webhook data.

## Link Creation Parameters

### Required

  * `title` - Title of the Link, be concise.
  * `description` - Describe what your customers will get, you can add terms and conditions and any other relevant information here. Markdown is supported, popular media URLs like Youtube, Flickr are auto-embedded.
  * `base_price` - Price of the Link. This may be 0, if you want to offer it for free. 
  * `currency` - Currency options are `INR` and `USD`. Note that you need to have a Bank Account in USA to accept USD currencies. 

### File and Cover Image
  * `file_upload` - Full path to the file you want to sell. This file will be available only after successful payment.
  * `cover_image` - Full path to the IMAGE you want to upload as a cover image.

### Quantity
  * `quantity` - Set to 0 for unlimited sales. If you set it to say 10, a total of 10 sales will be allowed after which the Link will be made unavailable.

### Post Purchase Note
  * `note` - A post-purchase note, will only displayed after successful payment. Will also be included in the ticket/ receipt that is sent as attachment to the email sent to buyer. This will not be shown if the payment fails.

### Event
  * `start_date` - Date-time when the event is beginning. Format: `YYYY-MM-DD HH:mm`
  * `end_date` - Date-time when the event is ending. Format: `YYYY-MM-DD HH:mm`
  * `venue` - Address of the place where the event will be held.
  * `timezone` - Timezone of the venue. Example: Asia/Kolkata

### Redirects and Webhooks
  * `redirect_url` - This can be a Thank-You page on your website. Buyers will be redirected to this page after successful payment.
  * `webhook_url` - Set this to a URL that can accept POST requests made by Instamojo server after successful payment.
  * `enable_pwyw` - set this to True, if you want to enable Pay What You Want. Default is False.
  * `enable_sign` - set this to True, if you want to enable Link Signing. Default is False. For more information regarding this, and to avail this feature write to support at instamojo.com.

Further documentation is available at https://www.instamojo.com/developers/
