//this file is generated by program, you should not change this file
//Created automatically on 15-07-19 23:41:14

#import <Fundation.h>
#import "WPModel.h"

@interface User : WPModel
@property (nonatomic, assign) NSInteger user_id;
@property (nonatomic, strong) NSString *name;
@property (nonatomic, assign) NSInteger is_male;
@property (nonatomic, assign) NSInteger create_time;
@end

@interface Game : WPModel
@property (nonatomic, assign) NSInteger game_id;
@property (nonatomic, strong) NSString *name;
@property (nonatomic, strong) NSString *play_url;
@property (nonatomic, assign) NSInteger player_num;
@property (nonatomic, assign) NSInteger create_time;
@end
