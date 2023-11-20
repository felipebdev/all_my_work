import { StyleSheet, Text, View, Image } from 'react-native'
import React, { useState } from 'react'
import { AntDesign } from '@expo/vector-icons';
import { FontAwesome } from '@expo/vector-icons';

const Publicaocards = (
    {
        post_pic,
        profile_image,
        username,
        likes,
        comments,
    }
) => {

    // console.log(post_pic, profile_pic, username, likes, comments)
    // console.log(comments)


    const [isliked, setIsliked] = useState(false)
    const [showcomments, setShowcomments] = useState(false)
    return (
        <View style={styles.container}>
            <View style={styles.c1}>
                <Image source={{ uri: profile_image }} style={styles.profilepic} />
                <Text style={styles.username}>{username}</Text>
                <Image source={require('../../images/opcao.png')} style={{marginLeft: 125,}}/>
            </View>
            <Image source={{ uri: post_pic }} style={styles.image} />

            <View style={styles.s2}>
                {
                    isliked ?
                        <View style={styles.s21}>
                            <View style={{marginTop: -150,marginStart: 15, width: 40, height: 40,
                            backgroundColor: 'white', alignItems: 'center', justifyContent: 'center',
                            borderRadius: 300}}>
                            <AntDesign name="heart" size={15} color="black" style={styles.iconliked} onPress={() => {
                                setIsliked(false)
                            }} />
                            </View>
        
                        </View>
                        :

                        <View style={styles.s21}>
                             <View style={{marginTop: -150,marginStart: 15, width: 40, height: 40,
                            backgroundColor: 'white', alignItems: 'center', justifyContent: 'center',
                            borderRadius: 300}}>
                            <AntDesign name="hearto" size={24} color="black" style={styles.iconliked}  onPress={() => {
                                setIsliked(true)
                            }} />
                            </View>
                        </View>
                }


                <View style={styles.s22}>
                
                    <FontAwesome name="comment" size={18} color="black"  onPress={
                        () => {
                            setShowcomments(!showcomments)
                        }
                    } />
                    
                </View>
            </View>


           
        </View>
    )
}

export default Publicaocards

const styles = StyleSheet.create({
    container: {
        backgroundColor: 'white',
        width: '90%',
        // height: 350,
        borderRadius: 20,
        marginVertical: 20,
        overflow: 'hidden',
        borderColor: 'white',
        borderWidth: 1,
        alignSelf: 'center'
    },
    c1: {
        width: '100%',
        flexDirection: 'row',
        alignItems: 'center',
        padding: 10,
        backgroundColor: 'white',
    },
    profilepic: {
        width: 40,
        height: 40,
        borderRadius: 30,
        borderColor: 'white',
        borderWidth: 1,
        marginTop: 5
    },
    username: {
        color: 'black',
        marginLeft: 10,
        fontSize: 17,
        fontWeight: 'bold',
    },
    image: {
        width: '95%',
        aspectRatio: 1,
        alignSelf: 'center',
        borderRadius: 20,
        marginTop: 10

    },
    s2: {
        width: '100%',
        flexDirection: 'row',
        backgroundColor: 'white',
        padding: 10,
        alignItems: 'center',
        marginTop: 20
    },
    s21: {
        // width: '100%',
        flexDirection: 'row',
        alignItems: 'center',
    },
    notliked: {
        color: 'grey',
        marginLeft: 5,
        fontSize: 25,
    },
    liked: {
        color: '#DC143C',
        marginLeft: 7,
        fontSize: 15,
        marginTop: -180
    },
    iconliked: {
        color: '#DC143C',
        fontSize: 20
    },
    s22: {
        width: 40, height: 40,
        elevation: 10, 
        marginTop: -140,
        marginStart: -55,
        backgroundColor: 'white',
        alignItems: 'center',
        justifyContent: 'center',
        borderRadius: 150
        
    },
    s3: {
        width: '100%',
        backgroundColor: 'white',
        padding: 10,
    },
    commentuser: {
        color: 'black',
        fontWeight: 'bold',
        fontSize: 17,

    },
    commenttext: {
        color: 'black',
        fontSize: 17,
        marginLeft: 5,
    },
    s31: {
        flexDirection: 'row',
        alignItems: 'center',
        marginVertical: 3,
    }

})