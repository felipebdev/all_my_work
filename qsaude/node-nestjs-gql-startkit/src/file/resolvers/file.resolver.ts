import { FileArgs } from '@app/file/models/file.input.model'
import { File } from '@app/file/models/file.model'
import { Args, Query, Resolver } from '@nestjs/graphql'
import { FileService } from '@app/file/services/file.service'

@Resolver()
export class FileResolver {
  constructor(private readonly fileService: FileService) {}

  @Query(() => [File], { nullable: false })
  async parametrizationFile(@Args() fileArgs: FileArgs): Promise<File[]> {
    return this.fileService.parametrizationFile(fileArgs)
  }
}
