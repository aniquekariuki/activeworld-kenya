<?php
// ============================================
// SAMPLE M-PESA CONFIG - RENAME TO mpesa-config.php
// ============================================
// Replace these with your actual credentials

class MpesaConfig {
    public static $consumerKey = 'WnpXczd7SzttVIhM16mFOJyvZDArkEx3ICow0wq0JjxNkeq7';
    public static $consumerSecret = '7lH8DtTKfYeQKJGVNsVdTdqdCGv8jA9yq8yCtNcIQLBXI0lQJl9tpvVI2YOaMGHw';
    public static $shortcode = '174379';
    public static $passkey = 'bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
    public static $environment = 'sandbox';
    public static $callbackUrl = 'https://activeworld.getenjoyment.net/api/mpesa-callback.php';
}
?>