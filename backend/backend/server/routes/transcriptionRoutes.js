const express = require('express');
const router = express.Router();
const multer = require('multer');
const fs = require('fs');
const OpenAI = require('openai');

const upload = multer({ dest: 'uploads/' });

const openai = new OpenAI({
    apiKey: process.env.OPENAI_API_KEY
});

router.post('/transcribe', upload.single('audio'), async (req, res) => {
    try {
        // Simulate processing delay
        await new Promise(resolve => setTimeout(resolve, 2000));

        // Demo response
        const demoResponse = {
            transcription: "Boss was very aggressive today, I'm scared and crying. Please help.",
            report: `**Incident Report**
**1. Initial Assessment**
- Urgency Level: High
- Risk Assessment: Immediate attention required

**2. Detected Keywords Analysis**
- **Boss:** Indicates a hierarchical relationship and potential power imbalance
- **Aggressive:** Implies hostile or threatening behavior
- **Scared:** Suggests fear or anxiety
- **Crying:** Indicates emotional distress

**3. Emotional State Analysis**
The presence of keywords "scared" and "crying" suggests significant emotional distress. The combination of these keywords with "aggressive" behavior indicates a potentially unsafe situation.

**4. Recommendations**
- Document all incidents
- Contact HR department if available
- Consider legal counsel
- Establish safety plan
- Seek support from trusted colleagues`
        };

        res.json(demoResponse);
    } catch (error) {
        console.error('Transcription error:', error);
        res.status(500).json({ error: error.message });
    }
});

module.exports = router;