require('dotenv').config();
const express = require('express');
const router = express.Router();

router.get('/key', (req, res) => {
    console.log('API Key requested, value:', process.env.GOOGLE_MAPS_API_KEY);
    res.json({ key: process.env.GOOGLE_MAPS_API_KEY });
});

module.exports = router;