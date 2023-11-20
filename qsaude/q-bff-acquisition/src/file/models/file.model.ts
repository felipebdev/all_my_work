import { ObjectType, Field } from '@nestjs/graphql'

@ObjectType({ description: 'return files' })
export class File {
  @Field()
  id: string

  @Field()
  mandatory: boolean

  @Field()
  name: string

  @Field()
  fileType: string

  @Field({ nullable: true })
  fileMimetype: string

  @Field({ nullable: true })
  fileOriginalname: string
  
  @Field({ nullable: true })
  fileSize: number
  
  @Field()
  origin: string
  
  @Field({ nullable: true })
  idProposal: string
  
  @Field({ nullable: true })
  idPerson: string
}