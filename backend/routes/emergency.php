const express = require('express');
const router = express.Router();
const twilio = require('twilio');

const checkTwilioConfig = () => {
    const config = {
        TWILIO_ACCOUNT_SID: process.env.TWILIO_ACCOUNT_SID,
        TWILIO_AUTH_TOKEN: process.env.TWILIO_AUTH_TOKEN,
        TWILIO_PHONE_NUMBER: process.env.TWILIO_PHONE_NUMBER,
        EMERGENCY_CONTACT_NUMBER: process.env.EMERGENCY_CONTACT_NUMBER
    };

    console.log('Twilio Config Check:', {
        SID_PREFIX: config.TWILIO_ACCOUNT_SID?.substring(0, 6),
        PHONE_FROM: config.TWILIO_PHONE_NUMBER,
        PHONE_TO: config.EMERGENCY_CONTACT_NUMBER
    });

    return config;
};

router.post('/emergency', async (req, res) => {
    try {
        const config = checkTwilioConfig();
        const client = twilio(config.TWILIO_ACCOUNT_SID, config.TWILIO_AUTH_TOKEN);
        
        console.log('Attempting to send emergency alerts...');
        const { message } = req.body;

        // Send SMS
        const smsResult = await client.messages.create({
            body: message,
            from: config.TWILIO_PHONE_NUMBER,
            to: config.EMERGENCY_CONTACT_NUMBER
        });
        console.log('SMS sent successfully:', smsResult.sid);

        // Make emergency call
        const callResult = await client.calls.create({
            twiml: '<Response><Say>Emergency alert! Someone needs immediate assistance.</Say><Pause length="1"/><Say>Location has been sent via SMS.</Say></Response>',
            to: config.EMERGENCY_CONTACT_NUMBER,
            from: config.TWILIO_PHONE_NUMBER
        });
        console.log('Call initiated successfully:', callResult.sid);

        res.json({ 
            success: true, 
            message: 'Emergency alerts sent',
            sms: smsResult.sid,
            call: callResult.sid
        });
    } catch (error) {
        console.error('Twilio Error Details:', {
            name: error.name,
            message: error.message,
            code: error.code,
            moreInfo: error.moreInfo
        });
        
        res.status(500).json({ 
            success: false, 
            error: error.message,
            details: error.code
        });
    }
});

module.exports = router;