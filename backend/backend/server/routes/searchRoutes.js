const express = require('express');
const router = express.Router();
const dataService = require('../services/dataService');

router.post('/search', async (req, res) => {
    const { service, sector, province } = req.body;
    
    try {
        const results = await dataService.searchCompanies(service);
        
        // Filter results if sector is specified
        const filteredResults = sector 
            ? results.filter(company => 
                company.description.toLowerCase().includes(sector.toLowerCase()))
            : results;

        console.log('Search Results:', filteredResults);
        res.json({ results: filteredResults });
    } catch (error) {
        console.error('Search error:', error);
        res.status(500).json({ error: error.message });
    }
});

module.exports = router;