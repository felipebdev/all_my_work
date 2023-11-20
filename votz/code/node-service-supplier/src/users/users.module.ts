import { Module } from '@nestjs/common';
import { UsersService } from '@app/users/services';
import { MongooseModule } from '@nestjs/mongoose';
import { User, UserSchema } from '@app/users/entities';
import { UserController } from './controllers/users.controller';
import { AccountService } from '@app/users/services';

@Module({
  imports: [
    MongooseModule.forFeature([
      {
        name: User.name,
        schema: UserSchema,
      },
    ]),
  ],
  providers: [AccountService, UsersService],
  controllers: [UserController],
  exports: [UsersService],
})
export class UsersModule {}
