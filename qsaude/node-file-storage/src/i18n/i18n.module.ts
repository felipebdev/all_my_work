import { Module } from '@nestjs/common'
import { AcceptLanguageResolver, I18nModule as NestI18nModule, QueryResolver } from 'nestjs-i18n'
import * as path from 'path'

@Module({
  imports: [
    NestI18nModule.forRoot({
      fallbackLanguage: 'en',
      loaderOptions: {
        path: path.join(__dirname, '/../i18n/lang'),
        watch: true
      },
      resolvers: [{ use: QueryResolver, options: ['lang', 'locale'] }, AcceptLanguageResolver]
    })
  ]
})
export class I18nModule {}
