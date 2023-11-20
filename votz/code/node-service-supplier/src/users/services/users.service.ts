import {
  Injectable,
  InternalServerErrorException,
  NotFoundException,
} from '@nestjs/common';
import { CreateUserDto, UpdateUserDto } from '@app/users/dto';
import { InjectModel } from '@nestjs/mongoose';
import { User } from '@app/users/entities';
import { Model } from 'mongoose';
import bcrypt from 'bcrypt';
import { AccountService } from './account.service';
import { VERIFICATION_CHANNEL_ENUM } from '../interfaces/account-verification.interface';
import { CacheService } from '../../common/services/cache.service';
import { MailService } from '../../common/services/mail.service';

@Injectable()
export class UsersService {
  constructor(
    @InjectModel(User.name) private readonly userModel: Model<User>,
    private readonly accountService: AccountService, // private readonly cacheService: CacheService, // private readonly mailService: MailService,
  ) {}

  async findOne(email: string): Promise<User> {
    try {
      const user = this.userModel.findOne({ email });
      if (!user) throw new NotFoundException(`User ${email} not found`);
      return user;
    } catch (error) {
      throw new InternalServerErrorException(error);
    }
  }

  async create(createUserDto: CreateUserDto) {
    try {
      const { password } = createUserDto;
      const hashedPassword = await bcrypt.hash(password, 10);
      const user: CreateUserDto = {
        ...createUserDto,
        password: hashedPassword,
      };
      const userModel = new this.userModel(user);
      await userModel.save();
      const code = String(Math.floor(100000 + Math.random() * 900000));
      return this.accountService.sendVerificationCode({
        channel: VERIFICATION_CHANNEL_ENUM.EMAIL,
        name: user.first_name,
        code,
      });
    } catch (error) {
      throw new InternalServerErrorException(error);
    }
  }

  async update(id: string, updateUserDto: UpdateUserDto) {
    const existingUser = await this.userModel
      .findOneAndUpdate({ _id: id }, { $set: updateUserDto }, { new: true })
      .exec();

    if (!existingUser) {
      throw new NotFoundException(`User #${id} not found`);
    }
    return existingUser;
  }
}
