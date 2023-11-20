export enum BigIDRoutes {
  OCR = '/VerifyID'
}

export interface BigIDValidatedDocument {
  DocInfo: {
    CPF: string
    DOCTYPE: string
    EXPEDITIONDATE: string
    IDENTIFICATIONNUMBER: string
    SIDE: string
  }
  EstimatedInfo: object
  TicketId: string
  ResultCode: number
  ResultMessage: string
}

export enum BigIDExceptionsMessages {
  NODOCTYPE = 'O documento enviado não é válido ou a imagem é de baixa qualidade.',
  NOCPF = 'A imagem enviada não contém o número de CPF ou é de baixa qualidade',
  DIFFERENT_DOCUMENT = 'O Documento informado não pertence ao usuário'
}
