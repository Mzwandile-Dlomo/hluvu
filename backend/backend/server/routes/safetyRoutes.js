const express = require('express');
const router = express.Router();
const { GoogleGenerativeAI } = require('@google/generative-ai');

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

router.post('/analyze-incident', async (req, res) => {
    try {
        const { keywords, timestamp } = req.body;
        const model = genAI.getGenerativeModel({ model: 'gemini-pro' });

        const prompt = `As a safety incident analyzer, create a detailed report based on the following keywords detected: ${keywords}. 
        Time of incident: ${timestamp}
        
        Please structure the report as follows:
        1. Initial Assessment (urgency level)
        2. Detected Keywords Analysis
        3. Emotional State Analysis
        4. Potential Situation Assessment
        5. Risk Level
        6. Immediate Recommendations
        7. Follow-up Actions
        
        Format this as a clear, professional incident report.`;

        const result = await model.generateContent(prompt);
        const response = await result.response;

        res.json({ 
            analysis: response.text(),
            timestamp: timestamp,
            detectedKeywords: keywords
        });
    } catch (error) {
        res.status(500).json({ error: error.message });
    }
});

router.post('/chat', async (req, res) => {
    try {
        const { message, isPostIncident } = req.body;
        const model = genAI.getGenerativeModel({ model: 'gemini-pro' });

        let prompt;
        if (isPostIncident) {
            prompt = `As a supportive AI assistant helping someone who has reported workplace abuse, 
            respond to their message: "${message}"
            
            Context: The user has reported workplace abuse involving their boss with aggressive behavior.
            
            Guidelines:
            - Show empathy and understanding
            - Provide specific, actionable advice
            - Prioritize their safety and well-being
            - Reference relevant support services or legal options
            - Be clear and direct in recommendations
            
            Format the response in a clear, structured manner using markdown-style formatting with ** for emphasis.`;
        } else {
            prompt = `As an AI assistant, respond to: "${message}"
            Format the response in a clear manner using markdown-style formatting with ** for emphasis.`;
        }

        const result = await model.generateContent(prompt);
        const response = await result.response;

        res.json({ 
            response: response.text(),
            isFormatted: true
        });
    } catch (error) {
        console.error('Error in chat route:', error);
        res.status(500).json({ 
            error: 'Failed to process message',
            details: error.message 
        });
    }
});

module.exports = router;