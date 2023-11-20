import { Schema } from 'dynamoose'

export const FileEntity = new Schema({
  id: {
    type: String,
    hashKey: true
  },

  fileType: { type: String },

  idPerson: { type: String },

  idProposal: { type: String },

  origin: { type: String },

  fileUrl: { type: String },

  fileMimetype: { type: String },

  fileOriginalname: { type: String },

  fileSize: { type: Number },
})
