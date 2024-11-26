const aiService = require('../services/aiService');

class AIController {
    async analyzeText(req, res) {
        try {
            const { text, context } = req.body;
            if (!text) {
                return res.status(400).json({ error: 'No text provided' });
            }

            const analysis = await aiService.analyzeContent(text, context);
            res.json({ analysis });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    }
}

module.exports = new AIController();