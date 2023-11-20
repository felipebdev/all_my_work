export interface IFilesKey {
  id: string
}

export interface IFiles extends IFilesKey {
  id: string
  fileType: string
  idPerson: string
  idProposal: string
  origin: string
  fileUrl: string
  fileMimetype: string
  fileOriginalname: string
  fileSize: number
}
