import { Body, Controller, Param, Patch, Post } from '@nestjs/common';
import { CreateUserDto, UpdateUserDto } from '@app/users/dto';
import { UsersService } from '@app/users/services';
import { AccountService } from '@app/users/services';
import { AccountVerificationDto } from '../dto/account-verification.dto';

@Controller('user')
export class UserController {
  constructor(
    private readonly userService: UsersService,
    private readonly accountService: AccountService,
  ) {}

  @Post()
  createUser(@Body() createUserDto: CreateUserDto) {
    return this.userService.create(createUserDto);
  }

  @Patch(':id')
  updateUser(@Param('id') id: string, @Body() updateUserDto: UpdateUserDto) {
    return this.userService.update(id, updateUserDto);
  }

  @Post('/account/confirm')
  async checkAccount(@Body() accountVerificationDto: AccountVerificationDto) {
    return this.accountService.checkCode(accountVerificationDto);
  }
}
