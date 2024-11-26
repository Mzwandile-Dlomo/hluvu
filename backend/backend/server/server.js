const express = require('express');
const dotenv = require('dotenv');
const path = require('path');
const fs = require('fs');
const emergencyRouter = require('./routes/emergency');
const mapsRouter = require('./routes/maps');
const safetyRouter = require('./routes/safetyRoutes');
const OpenAI = require('openai');
const multer = require('multer');
const upload = multer({ dest: 'uploads/' });

// Load environment variables with absolute path
dotenv.config({ path: path.resolve(__dirname, '../../.env') });

// Log environment variable status
console.log('Environment Check:', {
    NODE_ENV: process.env.NODE_ENV,
    PWD: process.env.PWD,
    ENV_PATH: path.resolve(__dirname, '../../.env'),
    TWILIO_SID_EXISTS: !!process.env.TWILIO_ACCOUNT_SID,
    TWILIO_TOKEN_EXISTS: !!process.env.TWILIO_AUTH_TOKEN
});

const app = express();
app.use(express.json());

// Enable CORS
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Origin, X-Requested-With, Content-Type, Accept');
    next();
});

// Use emergency routes
app.use('/api', emergencyRouter);

// Add the maps routes
app.use('/api/maps', mapsRouter);

// Add the safety routes
app.use('/api', safetyRouter);

// Initialize OpenAI
const openai = new OpenAI({
    apiKey: process.env.OPENAI_API_KEY
});

// Add this new route
app.post('/api/transcribe', upload.single('audio'), async (req, res) => {
    try {
        if (!req.file) {
            throw new Error('No audio file received');
        }

        const transcription = await openai.audio.transcriptions.create({
            file: fs.createReadStream(req.file.path),
            model: "whisper-1",
        });

        // Generate incident report
        const completion = await openai.chat.completions.create({
            model: "gpt-3.5-turbo",
            messages: [{
                role: "system",
                content: "You are an incident report analyzer. Extract the following from the text: date/time, type of abuse, perpetrator, location, and create a structured report."
            }, {
                role: "user",
                content: transcription.text
            }],
        });

        fs.unlinkSync(req.file.path); // Clean up
        res.json({ 
            transcription: transcription.text,
            report: completion.choices[0].message.content 
        });
    } catch (error) {
        console.error('Transcription error:', error);
        if (req.file && fs.existsSync(req.file.path)) {
            fs.unlinkSync(req.file.path);
        }
        res.status(500).json({ error: error.message || 'Failed to process audio' });
    }
});

const PORT = process.env.PORT || 3000;
app.listen(PORT, () => {
    console.log(`Server running on port ${PORT}`);
    console.log('Environment variables loaded:', {
        TWILIO_CONFIGURED: !!process.env.TWILIO_ACCOUNT_SID,
        EMERGENCY_NUMBER_CONFIGURED: !!process.env.EMERGENCY_CONTACT_NUMBER
    });
});