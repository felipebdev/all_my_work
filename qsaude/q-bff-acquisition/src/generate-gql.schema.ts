import { NestFactory } from '@nestjs/core'
import { GraphQLSchemaBuilderModule, GraphQLSchemaFactory } from '@nestjs/graphql'
import { printSchema } from 'graphql'
import { writeFileSync } from 'fs'
import { join } from 'path'
import { LeadResolver } from './lead/resolvers/lead.resolver'

async function generateSchema() {
  const app = await NestFactory.create(GraphQLSchemaBuilderModule)
  await app.init()

  const gqlSchemaFactory = app.get(GraphQLSchemaFactory)
  const schema = await gqlSchemaFactory.create([LeadResolver])
  await writeFileSync(join(process.cwd(), 'src/schema.gql'), printSchema(schema))
}

generateSchema()
