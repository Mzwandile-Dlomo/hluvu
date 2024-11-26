const pdfParse = require('pdf-parse');

class PDFService {
    async extractText(pdfBuffer) {
        try {
            const data = await pdfParse(pdfBuffer);
            return data.text;
        } catch (error) {
            throw new Error('Failed to extract text from PDF: ' + error.message);
        }
    }
}

module.exports = new PDFService();