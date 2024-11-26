const { GoogleGenerativeAI } = require('@google/generative-ai');
require('dotenv').config();

class AIService {
    constructor() {
        this.genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);
        this.model = this.genAI.getGenerativeModel({ model: 'gemini-pro' });
    }

    async analyzeContent(text, context) {
        try {
            const prompt = this.buildPrompt(context, text);
            const result = await this.model.generateContent(prompt);
            const response = await result.response;
            return this.formatResponse(context, response.text());
        } catch (error) {
            throw new Error('AI analysis failed: ' + error.message);
        }
    }

    formatResponse(context, text) {
        const intro = `<div class="ai-response">
            <div class="response-header">
                <div class="bot-avatar">
                    <i data-lucide="bot" size="24"></i>
                </div>
                <span class="bot-name">Venture-Bot</span>
            </div>
            <div class="response-content">`;

        // Format the greeting and assessment
        let formattedText = text.replace(/(EXCELLENT|GOOD|FAIR)/g, '<span class="assessment-$1">$1</span>');

        // Format section headers with icons
        const sectionIcons = {
            'Market Need': 'target',
            'Innovative Solution': 'lightbulb',
            'Business Model': 'briefcase',
            'Implementation Plan': 'git-branch',
            'Recommendations': 'check-circle'
        };

        Object.entries(sectionIcons).forEach(([section, icon]) => {
            formattedText = formattedText.replace(
                new RegExp(`${section}`, 'g'),
                `<h3 class="section-header">
                    <i data-lucide="${icon}" size="20"></i>
                    ${section}
                </h3>`
            );
        });

        // Format bullet points
        formattedText = formattedText.replace(/•(.*)/g, '<div class="bullet-point"><i data-lucide="check" size="16"></i>$1</div>');

        return `${intro}${formattedText}</div></div>`;
    }

    buildPrompt(keywords, timestamp, location) {
        return `Generate a detailed incident report based on the following:
        Time: ${timestamp}
        Location: ${location}
        Keywords Detected: ${keywords}

        Format the report with these sections:
        **1. Initial Assessment**
        - Urgency Level
        - Risk Assessment

        **2. Detected Keywords Analysis**
        - Analyze each keyword and its implications

        **3. Emotional State Analysis**
        - Analyze emotional indicators
        - Assess psychological state

        **4. Recommendations**
        - Immediate actions needed
        - Support resources
        - Safety measures`;
    }
}

module.exports = new AIService();