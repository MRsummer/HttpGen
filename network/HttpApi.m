//this file is generated by program, you should not change this file
//Created automatically on 15-07-19 23:41:14

#import <Fundation.h>
#import "HttpEntity.h"

@implementation HttpEntity

+getUserInfoWithUid:(NSInteger)uid Sid:(NSString *)sid Success:(void (^)(User *))apisuccess Failure:(void (^)(NSString *))apifailure{
    NSMutableDictionary* params = [[NSMutableDictionary alloc] init];
    NSNumber *uidNumber = [[NSNumber alloc] initWithInteger:uid];
    [params setValue:uidNumber forKey:@"uid"];
    [params setVlaue:sid forKey:"@sid"];
    
    [[WPHTTPClient sharedClient] POSTRequestWithWebAPI:@"user_api/get_user_info" parameters:params success:^(id result) {
        apisuccess([ modelFromDictionary: result[@"user"]]);
    } failure:^(NSString* errmsg) {
        apifailure(errmsg);
    }];
}

+isSingerWithUid:(NSInteger)uid Success:(void (^)(NSInteger))apisuccess Failure:(void (^)(NSString *))apifailure{
    NSMutableDictionary* params = [[NSMutableDictionary alloc] init];
    NSNumber *uidNumber = [[NSNumber alloc] initWithInteger:uid];
    [params setValue:uidNumber forKey:@"uid"];
    
    [[WPHTTPClient sharedClient] POSTRequestWithWebAPI:@"user_api/is_singer" parameters:params success:^(id result) {
        apisuccess([result[@"is_singer"] integerValue]);
    } failure:^(NSString* errmsg) {
        apifailure(errmsg);
    }];
}

+gameStartWithGameId:(NSInteger)game_id Success:(void (^)(NSInteger))apisuccess Failure:(void (^)(NSString *))apifailure{
    NSMutableDictionary* params = [[NSMutableDictionary alloc] init];
    NSNumber *game_idNumber = [[NSNumber alloc] initWithInteger:game_id];
    [params setValue:game_idNumber forKey:@"game_id"];
    
    [[WPHTTPClient sharedClient] POSTRequestWithWebAPI:@"game_api/game_start" parameters:params success:^(id result) {
        apisuccess([result[@"game_start"] integerValue]);
    } failure:^(NSString* errmsg) {
        apifailure(errmsg);
    }];
}

@end
