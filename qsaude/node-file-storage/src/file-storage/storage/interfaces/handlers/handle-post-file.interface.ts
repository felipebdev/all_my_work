import { IFile } from '@app/file-storage/storage/interfaces/models/file.interface'
import { IPostFileOptions } from '@app/file-storage/storage/interfaces/models/post-file-options.interface'
import { IPostFileResponse } from '@app/file-storage/storage/interfaces/models/post-file-response.interface'

export interface IHandlerPostFile {
  handlePostFile(appKey: string, file: IFile, options?: IPostFileOptions): Promise<IPostFileResponse>
}
