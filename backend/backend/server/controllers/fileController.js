const pdfService = require('../services/pdfService');
const aiService = require('../services/aiService');

class FileController {
    async processFile(req, res) {
        try {
            if (!req.file) {
                return res.status(400).json({ error: 'No file uploaded' });
            }

            const { context } = req.body;
            const pdfText = await pdfService.extractText(req.file.buffer);
            const analysis = await aiService.analyzeContent(pdfText, context);

            res.json({ analysis });
        } catch (error) {
            res.status(500).json({ error: error.message });
        }
    }
}

module.exports = new FileController();