const fs = require('fs').promises;
const path = require('path');

class DataService {
    async readDataFile() {
        try {
            const filePath = path.join(__dirname, '../../Data/data.txt');
            const data = await fs.readFile(filePath, 'utf8');
            return this.parseData(data);
        } catch (error) {
            throw new Error('Failed to read data file: ' + error.message);
        }
    }

    parseData(data) {
        const sections = {};
        let currentTitle = '';
        let currentList = [];

        data.split('\n').forEach(line => {
            line = line.trim();
            if (line.startsWith('Title :') || line.startsWith('tITLE =')) {
                if (currentTitle) {
                    sections[currentTitle] = currentList;
                }
                currentTitle = line.split('=')[1]?.trim() || line.split(':')[1]?.trim();
                currentList = [];
            } else if (line && !line.includes('TRACXN') && !line.includes('INCUBATORLIST')) {
                const colonIndex = line.indexOf(':');
                const name = colonIndex > -1 ? line.substring(0, colonIndex).trim() : line;
                const description = colonIndex > -1 ? line.substring(colonIndex + 1).trim() : line;
                
                if (name) {
                    currentList.push({
                        name: name,
                        description: description || name
                    });
                }
            }
        });

        if (currentTitle && currentList.length > 0) {
            sections[currentTitle] = currentList;
        }

        return sections;
    }

    async searchCompanies(service) {
        const data = await this.readDataFile();
        const searchKey = service === 'incubation' ? 'INCUBATION' : 'Investment';
        return data[searchKey] || [];
    }
}

module.exports = new DataService();