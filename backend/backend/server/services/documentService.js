const { db } = require('../config/firebase');
const { Timestamp } = require('firebase-admin/firestore');

class DocumentService {
  async storeDocument(userId, file, context, content) {
    try {
      const docRef = await db.collection('documents').add({
        userId,
        fileName: file.originalname,
        fileType: file.mimetype,
        context: context,
        content: content,
        uploadedAt: Timestamp.now()
      });
      return docRef.id;
    } catch (error) {
      throw new Error('Failed to store document: ' + error.message);
    }
  }

  async getUserDocuments(userId, category = null) {
    try {
      let query = db.collection('documents')
        .where('userId', '==', userId)
        .orderBy('uploadedAt', 'desc');
      
      if (category) {
        query = query.where('category', '==', category);
      }
      
      const snapshot = await query.get();
      return snapshot.docs.map(doc => ({
        id: doc.id,
        ...doc.data()
      }));
    } catch (error) {
      throw new Error('Failed to fetch documents: ' + error.message);
    }
  }
}

module.exports = new DocumentService();