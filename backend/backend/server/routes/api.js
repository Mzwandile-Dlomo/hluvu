const express = require('express');
const multer = require('multer');
const fileController = require('../controllers/fileController');

const router = express.Router();
const upload = multer({ storage: multer.memoryStorage() });

router.post('/analyze', upload.single('file'), fileController.processFile);

module.exports = router;