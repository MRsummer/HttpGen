//this file is automatically generated by program, you should not change this file

#import <Fundation.h>
#import "WPHttpClient.h"
#import "HttpEntity"

@interface HttpEntity : NSObject
+getUserInfoWithUid:(NSInteger)uid Sid:(NSString *)sid Success:(void (^)(User *))apisuccess Failure:(void (^)(NSString *))apifailure;
+isSingerWithUid:(NSInteger)uid Success:(void (^)(NSInteger))apisuccess Failure:(void (^)(NSString *))apifailure;
+gameStartWithGameId:(NSInteger)game_id Success:(void (^)(NSInteger))apisuccess Failure:(void (^)(NSString *))apifailure;
@end
