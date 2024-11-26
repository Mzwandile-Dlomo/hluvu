const express = require('express');
const router = express.Router();
const geminiService = require('../services/geminiService');

router.post('/analyze-route', async (req, res) => {
    try {
        const { startLocation, endLocation } = req.body;
        
        if (!startLocation || !endLocation) {
            return res.status(400).json({
                error: 'Both start and end locations are required',
                dangerZones: []
            });
        }

        const dangerZones = await geminiService.analyzeDangerZones(startLocation, endLocation);
        
        if (!Array.isArray(dangerZones) || dangerZones.length === 0) {
            return res.status(500).json({
                error: 'Invalid response format from AI service',
                dangerZones: []
            });
        }
        
        res.json({ dangerZones });
    } catch (error) {
        console.error('Route analysis error:', error);
        res.status(500).json({ 
            error: error.message,
            dangerZones: []
        });
    }
});

module.exports = router;