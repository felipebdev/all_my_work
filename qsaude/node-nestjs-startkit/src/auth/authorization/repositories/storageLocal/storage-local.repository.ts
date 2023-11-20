import { Injectable } from "@nestjs/common";
import { AbstractRepository } from "../abstract-repository.repository";

@Injectable()
export class StorageLocalRepository implements AbstractRepository<string> {
  private registry: string[]  = ['admin']

  async findAll(): Promise<string[]> {
    return this.registry
  }

}