//this file is automatically generated by program, you should not change this file

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
